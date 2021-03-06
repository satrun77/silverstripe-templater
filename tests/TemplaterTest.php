<?php

/**
 * TemplaterTest contains test cases for the module classes
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 * @package templater
 */
class TemplaterTest extends FunctionalTest
{
    protected static $fixture_file = 'TemplaterTest.yml';

    public function testTheme()
    {
        // Create page object
        $page = $this->createPage('blue');
        SiteConfig::current_site_config()->Theme = $page->Theme;

        // Assert content
        $this->assertContains('Theme Blue page content', $page->Content);

        // Assert page content
        $response = Director::test(Director::makeRelative($page->Link()));
        $this->assertContains('Theme Blue page content', $response->getBody());
        $this->assertContains('I\'m in the Blue Theme', $response->getBody());
    }

    public function testTemplate()
    {
        // Create page object
        $page = $this->createPage('hello');

        // Assert content
        $this->assertContains('Hi Hello page', $page->Content);

        // Assert page content
        $response = Director::test(Director::makeRelative($page->Link()));
        $this->assertContains('hello', $response->getBody());
        $this->assertNotContains('Hi Hello page', $response->getBody());
    }

    public function testNewPageTypeFields()
    {
        $page = $this->createPage('blue');
        $fields = $page->getCMSFields();

        $this->assertNotEmpty($fields);
        $this->assertNotEmpty($fields->dataFieldByName('Theme'));
        $this->assertNotEmpty($fields->dataFieldByName('PageTemplate'));
    }

    public function testDisabledOption()
    {
        // Enable module to error pages only
        Config::inst()->remove('Templater', 'enabled_for_pagetypes');
        Config::inst()->update('Templater', 'enabled_for_pagetypes', ['ErrorPage']);

        $page = $this->createPage('blue');
        $fields = $page->getCMSFields();

        $this->assertEmpty($fields->dataFieldByName('Theme'));
        $this->assertEmpty($fields->dataFieldByName('PageTemplate'));

        // Assert page content
        $response = Director::test(Director::makeRelative($page->Link()));
        $this->assertContains('Theme Blue page content', $response->getBody());
        $this->assertNotContains('I\'m in the Blue Theme', $response->getBody());

        // Enable module to all pages
        Config::inst()->remove('Templater', 'enabled_for_pagetypes');
        Config::inst()->update('Templater', 'enabled_for_pagetypes', 'all');
    }

    protected function createPage($name)
    {
        $page = $this->objFromFixture('Page', $name);

        // Login admin
        $this->logInWithPermission('ADMIN');

        // Assert: Publish page
        $published = $page->doPublish();
        $this->assertTrue($published);
        Member::currentUser()->logOut();

        return $page;
    }
}
